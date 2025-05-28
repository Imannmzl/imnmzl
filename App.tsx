
import React, { useState, useEffect } from 'react';
import { QUESTS, INITIAL_PLAYER_STATE, ALL_BADGES } from './constants';
import { Player, Item, Quest } from './types';
import Header from './components/Header';
import InventoryDisplay from './components/InventoryDisplay';
import QuestView from './components/QuestView';
import Notification from './components/Notification';

const App: React.FC = () => {
  const [player, setPlayer] = useState<Player>(INITIAL_PLAYER_STATE);
  const [currentQuestIndex, setCurrentQuestIndex] = useState(0);
  const [currentStageIndex, setCurrentStageIndex] = useState(0);
  const [notification, setNotification] = useState<{ message: string; type: 'success' | 'error' | 'info' } | null>(null);
  const [gameComplete, setGameComplete] = useState(false);

  const currentQuest = QUESTS[currentQuestIndex];

  const showNotification = (message: string, type: 'success' | 'error' | 'info' = 'info') => {
    setNotification({ message, type });
  };

  const handleStageComplete = (pointsAwarded: number, itemCollected?: Item) => {
    let newPoints = player.points + (pointsAwarded || 0);
    let newInventory = [...player.inventory];
    let newBadges = [...player.badges];

    if (itemCollected && !newInventory.find(i => i.id === itemCollected.id)) {
      newInventory.push(itemCollected);
      showNotification(`Item diperoleh: ${itemCollected.name}!`, 'success');
    }
    
    if (pointsAwarded > 0) {
        showNotification(`Poin +${pointsAwarded}!`, 'info');
    }

    const nextStageIndex = currentStageIndex + 1;
    if (currentQuest && nextStageIndex < currentQuest.stages.length) {
      setCurrentStageIndex(nextStageIndex);
    } else { // Quest completed
      if (currentQuest && !newBadges.includes(currentQuest.badgeName)) {
        newBadges.push(currentQuest.badgeName);
        showNotification(`Lencana diperoleh: ${currentQuest.badgeName}!`, 'success');
      }
      
      const nextQuestIndex = currentQuestIndex + 1;
      if (nextQuestIndex < QUESTS.length) {
        setCurrentQuestIndex(nextQuestIndex);
        setCurrentStageIndex(0);
      } else {
        setGameComplete(true);
        if (!newBadges.includes("Master of Networks")) {
            newBadges.push("Master of Networks");
            showNotification('Selamat! Kamu mendapatkan lencana "Master of Networks"!', 'success');
        }
        showNotification('Selamat! Semua misi telah diselesaikan!', 'success');
      }
    }
    setPlayer({ points: newPoints, inventory: newInventory, badges: newBadges });
  };

  const handleQuizSubmit = (allCorrect: boolean, pointsFromQuiz: number) => {
    setPlayer(prev => ({ ...prev, points: prev.points + pointsFromQuiz }));
    if (pointsFromQuiz > 0) {
        showNotification(`Kuis selesai! Poin +${pointsFromQuiz}`, allCorrect ? 'success' : 'info');
    } else {
        showNotification('Kuis selesai.', 'info');
    }
    // Stage progression is handled by onStageComplete, called after QuizModal closes.
  };
  
  const restartGame = () => {
    setPlayer(INITIAL_PLAYER_STATE);
    setCurrentQuestIndex(0);
    setCurrentStageIndex(0);
    setGameComplete(false);
    showNotification('Permainan dimulai ulang!', 'info');
  };

  if (gameComplete) {
    return (
      <div className="min-h-screen bg-slate-900 flex flex-col items-center justify-center p-4 text-slate-100">
        <Header player={player} />
        <main className="text-center my-10 p-8 bg-slate-800 rounded-xl shadow-2xl">
          <h1 className="text-4xl font-bold text-green-400 mb-4">Selamat, Agen Digital Terhebat!</h1>
          <p className="text-xl text-slate-300 mb-6">Kamu telah menyelesaikan semua misi dan menjadi ahli jaringan dan internet!</p>
          <p className="text-2xl text-amber-400 mb-2">Total Poin: {player.points}</p>
          <div className="my-4">
            <h3 className="text-xl font-semibold text-sky-400 mb-2">Lencana yang Diperoleh:</h3>
            <div className="flex flex-wrap justify-center gap-3">
              {player.badges.map(badge => (
                <span key={badge} className="bg-yellow-500 text-yellow-900 px-3 py-1 rounded-full text-sm font-medium">{badge}</span>
              ))}
            </div>
          </div>
           <div className="my-6">
            <h3 className="text-xl font-semibold text-sky-400 mb-2">Item Terkumpul:</h3>
             {player.inventory.length > 0 ? (
                <div className="flex flex-wrap justify-center gap-3">
                {player.inventory.map(item => (
                    <span key={item.id} className="bg-slate-700 text-slate-200 px-3 py-1 rounded-md text-sm flex items-center space-x-2" title={item.description}>
                    {item.icon} <span>{item.name}</span>
                    </span>
                ))}
                </div>
             ) : <p className="text-slate-400">Tidak ada item.</p>}
          </div>
          <button
            onClick={restartGame}
            className="mt-8 bg-sky-500 hover:bg-sky-600 text-white font-bold py-3 px-6 rounded-lg text-lg transition-colors duration-150 shadow-md hover:shadow-lg"
          >
            Main Lagi?
          </button>
        </main>
        <Notification message={notification?.message || null} type={notification?.type || 'info'} onDismiss={() => setNotification(null)} />
      </div>
    );
  }

  if (!currentQuest) {
    return <div className="min-h-screen flex items-center justify-center text-xl">Memuat petualangan...</div>;
  }

  return (
    <div className="min-h-screen bg-slate-900 flex flex-col">
      <Header player={player} />
      <main className="flex-grow container mx-auto p-4 flex flex-col lg:flex-row gap-6 items-start">
        <div className="w-full lg:w-2/3 order-2 lg:order-1">
          <QuestView
            quest={currentQuest}
            currentStageIndex={currentStageIndex}
            onStageComplete={handleStageComplete}
            onQuizSubmit={handleQuizSubmit}
          />
        </div>
        <div className="w-full lg:w-1/3 order-1 lg:order-2 lg:sticky lg:top-24"> {/* Sticky for desktop inventory */}
          <InventoryDisplay inventory={player.inventory} />
        </div>
      </main>
      <Notification message={notification?.message || null} type={notification?.type || 'info'} onDismiss={() => setNotification(null)} />
       <footer className="text-center p-4 text-sm text-slate-500">
        Petualangan Digital © 2024. Dibuat untuk edukasi.
      </footer>
    </div>
  );
};

export default App;
    