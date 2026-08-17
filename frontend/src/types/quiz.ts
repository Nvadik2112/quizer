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
  questions: Question[],
  questionAnswers: QuestionAnswer | null,
  activeIndex: number,
  isFinished: boolean,
  isLoading: boolean,
  error: string | null,
  getCurrentQuestion: () => Question;
  getCurrentQuestionAnswer: () => Answer | null;
  setDefaultAnswers: (questions: Question[]) => void;
  loadTests: () => Promise<void>;
  loadQuestions: (testId: string) => Promise<void>;
  nextQuestion: () => void;
  quizAnswerClick: (questionId: number, answerIndex: AnswerIndex) => Promise<void>;
  setTestDefault: () => void;
}