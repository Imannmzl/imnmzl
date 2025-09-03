// Shared JavaScript untuk semua slide - Hutan Harmoni

// API Configuration
const API_BASE_URL = '../api/leaderboard.php';
const USER_INFO_KEY = 'hh-user-info-v1';
const SESSION_KEY = 'hh-session-id-v1';
const AUDIO_ENABLED_KEY = 'hh-audio-enabled-v1';
const AUTOPLAY_ENABLED_KEY = 'hh-autoplay-enabled-v1';

// Global variables
let userInfo = { name: '', class: '' };
let sessionId = null;
let audioEnabled = true;
let autoplayEnabled = false;
let currentAudio = null;

// Load user info and session
function loadUserData() {
  try {
    const savedUserInfo = localStorage.getItem(USER_INFO_KEY);
    if (savedUserInfo) {
      userInfo = JSON.parse(savedUserInfo);
    }
    
    const savedSession = localStorage.getItem(SESSION_KEY);
    if (savedSession) {
      sessionId = savedSession;
    }
    
    const savedAudioEnabled = localStorage.getItem(AUDIO_ENABLED_KEY);
    if (savedAudioEnabled !== null) {
      audioEnabled = savedAudioEnabled === 'true';
    }
    
    const savedAutoplayEnabled = localStorage.getItem(AUTOPLAY_ENABLED_KEY);
    if (savedAutoplayEnabled !== null) {
      autoplayEnabled = savedAutoplayEnabled === 'true';
    }
    
    console.log('User data loaded:', { userInfo, sessionId, audioEnabled, autoplayEnabled });
  } catch (e) {
    console.warn('Failed to load user data:', e);
  }
}

// Toast functionality
function showToast(message) {
  const toast = document.getElementById('toast');
  if (toast) {
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 2400);
  }
}

// API Functions
async function apiCall(endpoint, data = null, method = 'GET') {
  try {
    console.log(`API Call: ${method} ${endpoint}`, data);
    
    const options = {
      method: method,
      headers: {
        'Content-Type': 'application/json',
      }
    };
    
    if (data && method !== 'GET') {
      options.body = JSON.stringify(data);
    }
    
    const url = method === 'GET' && data ? 
      `${endpoint}?${new URLSearchParams(data)}` : endpoint;
    
    const response = await fetch(url, options);
    const result = await response.json();
    
    if (!response.ok) {
      throw new Error(result.message || `HTTP ${response.status}`);
    }
    
    return result;
  } catch (error) {
    console.error('API Error:', error);
    return { success: false, message: error.message };
  }
}

// Update progress to backend
async function updateBackendProgress(totalMistakes = 0) {
  if (!sessionId) return;
  
  console.log('Updating backend progress:', { sessionId, totalMistakes });
  
  const result = await apiCall(API_BASE_URL, {
    action: 'update_progress',
    session_id: sessionId,
    total_mistakes: totalMistakes
  }, 'POST');
  
  console.log('Progress update result:', result);
  return result;
}

// Complete game on backend
async function completeGameOnBackend(totalMistakes = 0) {
  console.log('🎯 completeGameOnBackend called');
  console.log('sessionId:', sessionId);
  console.log('totalMistakes:', totalMistakes);
  
  if (!sessionId) {
    console.error('❌ No session ID found');
    showToast('⚠️ Sesi tidak ditemukan. Data tidak tersimpan.');
    return;
  }
  
  const mistakeCount = Number(totalMistakes) || 0;
  console.log('📤 Sending completion data to backend...');
  
  const result = await apiCall(API_BASE_URL, {
    action: 'complete_game',
    session_id: sessionId,
    total_mistakes: mistakeCount
  }, 'POST');
  
  console.log('📥 Backend completion result:', result);
  
  if (result.success) {
    const score = result.score || (100 - (mistakeCount * 10));
    const rank = result.rank || 1;
    showToast(`🎉 Skor: ${score}! Ranking: #${rank}`);
    console.log('✅ Game completion successful:', result);
  } else {
    console.error('❌ Game completion failed:', result.message);
    const localScore = Math.max(0, 100 - (mistakeCount * 10));
    showToast(`🎉 Skor lokal: ${localScore}! (Offline mode)`);
  }
  
  return result;
}

// Audio functionality
function playAudio(audioId) {
  if (!audioEnabled) return;
  
  stopCurrentAudio();
  
  const audio = document.getElementById(`audio-${audioId}`);
  if (audio && audio.src) {
    currentAudio = audio;
    audio.play().catch(e => console.warn('Audio play failed:', e));
  }
}

function stopCurrentAudio() {
  if (currentAudio) {
    currentAudio.pause();
    currentAudio.currentTime = 0;
    currentAudio = null;
  }
}

// Initialize shared functionality
document.addEventListener('DOMContentLoaded', () => {
  console.log('Shared JS loaded');
  loadUserData();
  
  // Setup audio controls if present
  const audioControls = document.querySelectorAll('.audio-controls button');
  audioControls.forEach(btn => {
    btn.addEventListener('click', () => {
      const audioId = btn.id.replace('btn-play-', '');
      playAudio(audioId);
    });
  });
  
  // Auto-scroll to top
  window.scrollTo({ top: 0, behavior: 'smooth' });
});