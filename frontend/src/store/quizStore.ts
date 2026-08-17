import { create } from "zustand";
import type {AnswerIndex, QuestionAnswer, QuizState} from "@/types/quiz.ts";

export const useQuizStore = create<QuizState>()((set, get) =>({
  tests: [],
  questions: [],
  questionAnswers: null,
  activeIndex: 0,
  isFinished: false,
  isLoading: false,
  error: null,

  getCurrentQuestion: () => {
    const { questions, activeIndex } = get();

    return questions[activeIndex] || null;
  },
  getCurrentQuestionAnswer: () => {
    const { questionAnswers, activeIndex } = get();
    const question = get().getCurrentQuestion();

    if (!question || !questionAnswers) {
      return null;
    }

    return questionAnswers[activeIndex];
  },
  setDefaultAnswers: (data) => {
    const initialAnswers: QuestionAnswer = {};

    data.forEach((_question, index) => {
      initialAnswers[index] = { answerIndex: null, status: '' };
    });

    set({ questionAnswers: initialAnswers });
  },
  loadTests: async() => {
    set({ isLoading: true, error: null });

    try {
      const response = await fetch('http://localhost:8000/tests');

      if (!response.ok) {
        set({
          error: 'Ошибка загрузки тестов',
          isLoading: false
        });

        return;
      }

      const data = await response.json();
      set({ tests: data });

      set({ isLoading: false });
    } catch (error) {
      set({
        error: error instanceof Error ? error.message : 'Неизвестная ошибка',
        isLoading: false
      });
      console.error('❌ Ошибка:', error)
    }
  },
  loadQuestions: async (testId: string) => {
    set({ isLoading: true, error: null });

    try {
      const response = await fetch(`http://localhost:8000/questions?testId=${testId}`);

      if (!response.ok) {
        set({
          error: 'Ошибка загрузки вопросов',
          isLoading: false
        });
      }

      const data = await response.json();
      set({ questions: data });

      const { setDefaultAnswers } = get();
      setDefaultAnswers(data);

      set({ isLoading: false });
    } catch (error) {
      set({
        error: error instanceof Error ? error.message : 'Неизвестная ошибка',
        isLoading: false
      });
      console.error('❌ Ошибка:', error);
    }
  },
  nextQuestion: () => {
    const { activeIndex, questions } = get();

    set({ activeIndex: activeIndex + 1 });

    if (activeIndex === questions.length - 1) {
      set({ isFinished: true });
    }
  },
  quizAnswerClick: async (questionId: number, answerIndex: AnswerIndex) => {
    const { questionAnswers, activeIndex } = get();
    if (!questionAnswers) {
      return;
    }

    if (questionAnswers && questionAnswers[activeIndex]?.answerIndex !== null) {
      return;
    }

    try {
      const response = await fetch(`http://localhost:8000/questions/${questionId}/check`, {
        method: 'POST',
        body: JSON.stringify({
          correctAnswerIndex: answerIndex
        }),
      });

      const data = await response.json();

      set((state) => {
        return {
          questionAnswers: {
            ...state.questionAnswers,
            [activeIndex]: {
              answerIndex: answerIndex,
              status: data ? 'success' : 'error'
            }
          }
        }
      });
    } catch (error) {
      console.error('❌ Ошибка:', error);
    }
  },
  setTestDefault: () => {
    const { questions, setDefaultAnswers } = get();
    set({ activeIndex: 0, isFinished: false });
    setDefaultAnswers(questions)
  },
}));