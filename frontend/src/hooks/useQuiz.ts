import { useQuery, useMutation } from '@tanstack/react-query';
import { quizApi } from '@/api/quiz';
import type { AnswerIndex } from '@/types/quiz';

export const useTests = () => {
  return useQuery({
    queryKey: ['tests'],
    queryFn: quizApi.loadTests,
    staleTime: 1000 * 60 * 5,
  });
};

export const useQuestions = (testId: string) => {
  return useQuery({
    queryKey: ['questions', testId],
    queryFn: async () => {
      const data = await quizApi.loadQuestions(testId);
      return data.sort((a, b) => a.position - b.position);
    },
    enabled: !!testId,
    staleTime: 1000 * 60 * 5,
  });
};

export const useCheckAnswer = () => {
  // const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({ questionId, answerIndex }: { questionId: number; answerIndex: AnswerIndex }) =>
      quizApi.checkAnswer(questionId, answerIndex),
    // onSuccess: (_,) => {
    //   void queryClient.invalidateQueries({ queryKey: ['questions'] });
    // },
    onError: (error) => {
      console.error('Ошибка проверки ответа:', error);
    },
  });
};