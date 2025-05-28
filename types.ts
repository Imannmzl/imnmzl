
export interface Item {
  id: string;
  name: string;
  description: string;
  icon: React.ReactNode; // For simple SVG icons or emojis
}

export interface QuizQuestion {
  question: string;
  options: string[];
  correctAnswer: string; // The string value of the correct option
  explanation: string;
}

export enum StageType {
  INFO = 'INFO',
  DIALOGUE = 'DIALOGUE',
  CHALLENGE = 'CHALLENGE',
  QUIZ = 'QUIZ',
  COLLECT_ITEM = 'COLLECT_ITEM',
  QUEST_COMPLETE = 'QUEST_COMPLETE'
}

export interface Stage {
  id: string;
  type: StageType;
  title?: string; // Optional title for the stage
  npcName?: string;
  dialogue?: string[] | { speaker: string; text: string }[];
  challengeDescription?: string;
  challengeComponent?: string; // Identifier for a specific challenge UI
  quizQuestions?: QuizQuestion[];
  infoText?: string | string[];
  itemToCollect?: Item;
  pointsAwarded?: number;
  backgroundImageUrl?: string; // For visual context
}

export interface Quest {
  id: string;
  title: string;
  description: string;
  badgeName: string;
  stages: Stage[];
  icon: React.ReactNode;
}

export interface Player {
  points: number;
  inventory: Item[];
  badges: string[]; // Names of badges earned
}

export interface NPC {
  id: string;
  name: string;
  avatarUrl?: string; // URL to an avatar image
}
    