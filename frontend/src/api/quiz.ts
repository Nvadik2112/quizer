import type { Question, Test } from '@/types/quiz';
import apiClient from "@/api/client.ts";

export const quizApi = {
  loadTests: async (): Promise<Test[]> => {
    const response = await apiClient.get<Test[]>('/tests');
    return response.data;
  },
  loadQuestions: async (testId: string): Promise<Question[]> => {
    const response = await apiClient.get<Question[]>('/questions', {
      params: { testId },
    });
    return response.data;
  },
  checkAnswer: async (questionId: number, answerIndex: number): Promise<boolean> => {
    const response = await apiClient.post<boolean>(
      `/questions/${questionId}/check`,
      { correctAnswerIndex: answerIndex }
    );

    return response.data;
  },
};