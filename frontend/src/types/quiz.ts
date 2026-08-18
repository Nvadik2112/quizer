export interface Test {
  id: number,
  title: string,
}
export interface Question {
  id: number;
  title: string;
  answers: string[];
  position: number
}

export const ANSWER_INDICES = [0, 1, 2, 3] as const;
export type AnswerIndex = typeof ANSWER_INDICES[number];

export interface Answer {
  answerIndex: AnswerIndex | null;
  status: '' | 'success' | 'error';
}

export type QuestionAnswer = Record<number, Answer>;

export interface QuizState {
  tests: Test[],
  questions: Question[];
  questionAnswers: QuestionAnswer | null;
  activeIndex: number;
  isFinished: boolean;
  getCurrentQuestion: () => Question | null;
  getCurrentAnswerStatus: () => Answer | null;
  setDefaultAnswers: (questions: Question[]) => void;
  nextQuestion: () => void;
  setQuestions: (questions: Question[]) => void;
  setTests: (tests: Test[]) => void;
  setTestDefault: () => void;
  setAnswerStatus: (activeIndex: number, answerIndex: AnswerIndex, isCorrect: boolean) => void;
}