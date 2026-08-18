import { create } from "zustand";
import type {Question, QuestionAnswer, QuizState, Test} from "@/types/quiz.ts";

export const useQuizStore = create<QuizState>()((set, get) =>({
  tests: [],
  questions: [],
  questionAnswers: null,
  activeIndex: 0,
  isFinished: false,

  getCurrentQuestion: () => {
    const { questions, activeIndex } = get();

    return questions[activeIndex] || null;
  },
  getCurrentAnswerStatus: () => {
    const { questionAnswers, activeIndex } = get();
    const question = get().getCurrentQuestion();

    if (!question || !questionAnswers) {
      return null;
    }

    return questionAnswers[activeIndex];
  },
  setQuestions: (data: Question[]) => {
    set({ questions: data });
  },
  setTests: (data: Test[]) => {
    set({ tests: data })
  },
  setDefaultAnswers: (data) => {
    const initialAnswers: QuestionAnswer = {};

    data.forEach((_question, index) => {
      initialAnswers[index] = { answerIndex: null, status: '' };
    });

    set({ questionAnswers: initialAnswers });
  },
  nextQuestion: () => {
    const { activeIndex, questions } = get();

    set({ activeIndex: activeIndex + 1 });

    if (activeIndex === questions.length - 1) {
      set({ isFinished: true });
    }
  },
  setTestDefault: () => {
    const { questions, setDefaultAnswers } = get();
    set({ activeIndex: 0, isFinished: false });
    setDefaultAnswers(questions)
  },
  setAnswerStatus: (activeIndex, answerIndex, isCorrect) => {
    set((state) => {
      return {
        questionAnswers: {
          ...state.questionAnswers,
          [activeIndex]: {
            answerIndex: answerIndex,
            status: isCorrect ? 'success' : 'error'
          }
        }
      }
    });
  }
}));