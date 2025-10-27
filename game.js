class RPGGame {
    constructor() {
        this.canvas = document.getElementById('gameCanvas');
        this.ctx = this.canvas.getContext('2d');
        
        // Game state
        this.isRunning = false;
        this.debugMode = false;
        this.gridEnabled = false;
        this.teleportMode = false;
        this.lastTime = 0;
        
        // Character properties
        this.character = {
            x: 100,
            y: 100,
            width: 32,
            height: 32,
            speed: 2,
            direction: 'down',
            isMoving: false,
            animationFrame: 0,
            bounceOffset: 0,
            shadowOffset: 2
        };
        
        // Camera properties
        this.camera = {
            x: 0,
            y: 0,
            zoom: 1.3,
            followSpeed: 0.1
        };
        
        // Map properties
        this.map = {
            width: 0,
            height: 0,
            image: null
        };
        
        // Input handling
        this.keys = {};
        this.touchInput = {
            isActive: false,
            direction: null
        };
        
        // Debug info
        this.debugInfo = {
            charPos: document.getElementById('charPos'),
            camPos: document.getElementById('camPos'),
            zoomLevel: document.getElementById('zoomLevel')
        };
        
        this.init();
    }
    
    async init() {
        await this.loadAssets();
        this.setupCanvas();
        this.setupEventListeners();
        this.setupDebugControls();
        this.start();
    }
    
    async loadAssets() {
        return new Promise((resolve) => {
            // Load map image
            this.map.image = new Image();
            this.map.image.onload = () => {
                this.map.width = this.map.image.width;
                this.map.height = this.map.image.height;
                console.log(`Map loaded: ${this.map.width}x${this.map.height}`);
                resolve();
            };
            this.map.image.src = 'map.jpg';
        });
    }
    
    setupCanvas() {
        this.resizeCanvas();
        window.addEventListener('resize', () => this.resizeCanvas());
    }
    
    resizeCanvas() {
        const rect = this.canvas.getBoundingClientRect();
        this.canvas.width = window.innerWidth;
        this.canvas.height = window.innerHeight;
        
        // Update camera bounds
        this.updateCameraBounds();
    }
    
    updateCameraBounds() {
        const canvasWidth = this.canvas.width;
        const canvasHeight = this.canvas.height;
        const zoomedMapWidth = this.map.width * this.camera.zoom;
        const zoomedMapHeight = this.map.height * this.camera.zoom;
        
        // Camera boundaries (top, left, right bounded, bottom free for D-pad)
        this.camera.bounds = {
            minX: Math.min(0, canvasWidth - zoomedMapWidth),
            maxX: 0,
            minY: Math.min(0, canvasHeight - zoomedMapHeight),
            maxY: 0 // Bottom is free for D-pad space
        };
    }
    
    setupEventListeners() {
        // Keyboard events
        document.addEventListener('keydown', (e) => this.handleKeyDown(e));
        document.addEventListener('keyup', (e) => this.handleKeyUp(e));
        
        // Touch events for D-pad
        const dpadButtons = document.querySelectorAll('.dpad-btn');
        dpadButtons.forEach(btn => {
            btn.addEventListener('touchstart', (e) => this.handleTouchStart(e));
            btn.addEventListener('touchend', (e) => this.handleTouchEnd(e));
            btn.addEventListener('mousedown', (e) => this.handleTouchStart(e));
            btn.addEventListener('mouseup', (e) => this.handleTouchEnd(e));
        });
        
        // Canvas click for teleport
        this.canvas.addEventListener('click', (e) => this.handleCanvasClick(e));
        
        // Prevent context menu
        this.canvas.addEventListener('contextmenu', (e) => e.preventDefault());
    }
    
    setupDebugControls() {
        const debugToggle = document.getElementById('debugToggle');
        const gridToggle = document.getElementById('gridToggle');
        const teleportMode = document.getElementById('teleportMode');
        const crosshair = document.getElementById('crosshair');
        
        debugToggle.addEventListener('click', () => {
            this.debugMode = !this.debugMode;
            const debugInfo = document.getElementById('debugInfo');
            debugInfo.classList.toggle('hidden');
            crosshair.classList.toggle('hidden', !this.debugMode);
        });
        
        gridToggle.addEventListener('click', () => {
            this.gridEnabled = !this.gridEnabled;
            gridToggle.textContent = `Grid: ${this.gridEnabled ? 'ON' : 'OFF'}`;
        });
        
        teleportMode.addEventListener('click', () => {
            this.teleportMode = !this.teleportMode;
            teleportMode.textContent = `Teleport: ${this.teleportMode ? 'ON' : 'OFF'}`;
        });
    }
    
    handleKeyDown(e) {
        this.keys[e.key.toLowerCase()] = true;
        e.preventDefault();
    }
    
    handleKeyUp(e) {
        this.keys[e.key.toLowerCase()] = false;
        e.preventDefault();
    }
    
    handleTouchStart(e) {
        e.preventDefault();
        const direction = e.target.dataset.direction;
        this.touchInput.isActive = true;
        this.touchInput.direction = direction;
    }
    
    handleTouchEnd(e) {
        e.preventDefault();
        this.touchInput.isActive = false;
        this.touchInput.direction = null;
    }
    
    handleCanvasClick(e) {
        if (!this.teleportMode) return;
        
        const rect = this.canvas.getBoundingClientRect();
        const clickX = e.clientX - rect.left;
        const clickY = e.clientY - rect.top;
        
        // Convert screen coordinates to world coordinates
        const worldX = (clickX - this.canvas.width / 2) / this.camera.zoom + this.character.x;
        const worldY = (clickY - this.canvas.height / 2) / this.camera.zoom + this.character.y;
        
        // Teleport character
        this.character.x = Math.max(0, Math.min(worldX, this.map.width - this.character.width));
        this.character.y = Math.max(0, Math.min(worldY, this.map.height - this.character.height));
        
        console.log(`Teleported to: ${Math.round(this.character.x)}, ${Math.round(this.character.y)}`);
    }
    
    updateInput() {
        let dx = 0;
        let dy = 0;
        
        // Keyboard input
        if (this.keys['arrowleft'] || this.keys['a']) dx = -1;
        if (this.keys['arrowright'] || this.keys['d']) dx = 1;
        if (this.keys['arrowup'] || this.keys['w']) dy = -1;
        if (this.keys['arrowdown'] || this.keys['s']) dy = 1;
        
        // Touch input
        if (this.touchInput.isActive) {
            switch (this.touchInput.direction) {
                case 'left': dx = -1; break;
                case 'right': dx = 1; break;
                case 'up': dy = -1; break;
                case 'down': dy = 1; break;
            }
        }
        
        // Update character movement
        if (dx !== 0 || dy !== 0) {
            this.character.isMoving = true;
            this.character.direction = this.getDirection(dx, dy);
            
            // Calculate new position
            const newX = this.character.x + dx * this.character.speed;
            const newY = this.character.y + dy * this.character.speed;
            
            // Boundary check
            if (newX >= 0 && newX <= this.map.width - this.character.width) {
                this.character.x = newX;
            }
            if (newY >= 0 && newY <= this.map.height - this.character.height) {
                this.character.y = newY;
            }
        } else {
            this.character.isMoving = false;
        }
    }
    
    getDirection(dx, dy) {
        if (dx < 0) return 'left';
        if (dx > 0) return 'right';
        if (dy < 0) return 'up';
        if (dy > 0) return 'down';
        return this.character.direction;
    }
    
    updateAnimation(deltaTime) {
        if (this.character.isMoving) {
            this.character.animationFrame += deltaTime * 0.01;
            this.character.bounceOffset = Math.sin(this.character.animationFrame) * 2;
        } else {
            this.character.bounceOffset = 0;
        }
    }
    
    updateCamera() {
        // Calculate target camera position (center on character)
        const targetX = this.character.x - this.canvas.width / 2 / this.camera.zoom;
        const targetY = this.character.y - this.canvas.height / 2 / this.camera.zoom;
        
        // Smooth camera follow
        this.camera.x += (targetX - this.camera.x) * this.camera.followSpeed;
        this.camera.y += (targetY - this.camera.y) * this.camera.followSpeed;
        
        // Apply camera bounds
        this.camera.x = Math.max(this.camera.bounds.minX, Math.min(this.camera.bounds.maxX, this.camera.x));
        this.camera.y = Math.max(this.camera.bounds.minY, Math.min(this.camera.bounds.maxY, this.camera.y));
    }
    
    render() {
        // Clear canvas
        this.ctx.fillStyle = '#000';
        this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);
        
        // Save context
        this.ctx.save();
        
        // Apply camera transform
        this.ctx.translate(-this.camera.x * this.camera.zoom, -this.camera.y * this.camera.zoom);
        this.ctx.scale(this.camera.zoom, this.camera.zoom);
        
        // Draw map
        if (this.map.image) {
            this.ctx.drawImage(this.map.image, 0, 0);
        }
        
        // Draw grid if enabled
        if (this.gridEnabled) {
            this.drawGrid();
        }
        
        // Draw character shadow
        this.ctx.fillStyle = 'rgba(0, 0, 0, 0.3)';
        this.ctx.fillRect(
            this.character.x + this.character.shadowOffset,
            this.character.y + this.character.height - 4 + this.character.shadowOffset,
            this.character.width,
            4
        );
        
        // Draw character (placeholder rectangle for now)
        this.ctx.fillStyle = '#ff6b6b';
        this.ctx.fillRect(
            this.character.x,
            this.character.y + this.character.bounceOffset,
            this.character.width,
            this.character.height
        );
        
        // Draw character face direction indicator
        this.ctx.fillStyle = '#fff';
        const faceX = this.character.direction === 'left' ? this.character.x : this.character.x + this.character.width - 4;
        this.ctx.fillRect(faceX, this.character.y + 8, 4, 4);
        
        // Restore context
        this.ctx.restore();
        
        // Draw debug info
        if (this.debugMode) {
            this.drawDebugInfo();
        }
    }
    
    drawGrid() {
        const gridSize = 25;
        const startX = Math.floor(this.camera.x / gridSize) * gridSize;
        const startY = Math.floor(this.camera.y / gridSize) * gridSize;
        const endX = startX + this.canvas.width / this.camera.zoom + gridSize;
        const endY = startY + this.canvas.height / this.camera.zoom + gridSize;
        
        this.ctx.strokeStyle = 'rgba(255, 255, 255, 0.1)';
        this.ctx.lineWidth = 1;
        
        // Vertical lines
        for (let x = startX; x <= endX; x += gridSize) {
            this.ctx.beginPath();
            this.ctx.moveTo(x, startY);
            this.ctx.lineTo(x, endY);
            this.ctx.stroke();
        }
        
        // Horizontal lines
        for (let y = startY; y <= endY; y += gridSize) {
            this.ctx.beginPath();
            this.ctx.moveTo(startX, y);
            this.ctx.lineTo(endX, y);
            this.ctx.stroke();
        }
    }
    
    drawDebugInfo() {
        // Update debug text
        this.debugInfo.charPos.textContent = `${Math.round(this.character.x)}, ${Math.round(this.character.y)}`;
        this.debugInfo.camPos.textContent = `${Math.round(this.camera.x)}, ${Math.round(this.camera.y)}`;
        this.debugInfo.zoomLevel.textContent = `${this.camera.zoom}x`;
    }
    
    gameLoop(currentTime) {
        if (!this.isRunning) return;
        
        const deltaTime = currentTime - this.lastTime;
        this.lastTime = currentTime;
        
        // Update game logic
        this.updateInput();
        this.updateAnimation(deltaTime);
        this.updateCamera();
        
        // Render
        this.render();
        
        // Continue loop
        requestAnimationFrame((time) => this.gameLoop(time));
    }
    
    start() {
        this.isRunning = true;
        this.lastTime = performance.now();
        requestAnimationFrame((time) => this.gameLoop(time));
        console.log('🎮 RPG Game Started!');
        console.log('Controls: Arrow keys/WASD or Virtual D-Pad');
        console.log('Debug: Click the bug button (top-right)');
    }
    
    stop() {
        this.isRunning = false;
    }
}

// Initialize game when page loads
window.addEventListener('load', () => {
    new RPGGame();
});