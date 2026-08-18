import './AnswersList.css'
import AnswerItem from "@/components/ActiveQuiz/AnswersList/AnswerItem/AnswerItem.tsx";
import { useQuizStore } from "@/store";
import { ANSWER_INDICES, type AnswerIndex } from "@/types/quiz.ts";
import { useCheckAnswer } from "@/hooks/useQuiz.ts";

const AnswersList = () => {
  const {
    mutate: checkAnswer,
    isPending
  } = useCheckAnswer();

  const {
    activeIndex,
    getCurrentQuestion,
    setAnswerStatus
  } = useQuizStore();

  const currentQuestion = getCurrentQuestion();

  if (!currentQuestion) {
    return;
  }

  const { answers, id } = currentQuestion;

  const handleAnswer = (answerIndex: AnswerIndex) => {
    checkAnswer(
      { questionId: id, answerIndex: answerIndex },
      {
        onSuccess: (isCorrect) => {
          setAnswerStatus(activeIndex, answerIndex, isCorrect);
        },
      }
    );
  };

  return (
    <ul className="AnswersList">
      {answers.map((answer, index) => {
        const typedIndex = index as AnswerIndex;

        if (!ANSWER_INDICES.includes(typedIndex)) {
          return null;
        }

        return (
          <AnswerItem
            key={index}
            index={typedIndex}
            answer={answer}
            isPending={isPending}
            handleAnswer={() => handleAnswer(typedIndex)}
          />
        )
      })}
    </ul>
  )
}

export default AnswersList;