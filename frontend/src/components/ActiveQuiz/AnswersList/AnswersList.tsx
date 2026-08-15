import './AnswersList.css'
import AnswerItem from "@/components/ActiveQuiz/AnswersList/AnswerItem/AnswerItem.tsx";
import { useQuizStore } from "@/store";
import {ANSWER_INDICES, type AnswerIndex} from "@/types/quiz.ts";

const AnswersList = () => {
  const {
    getCurrentQuestion,
  } = useQuizStore();

  const { answers } = getCurrentQuestion();

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
          />
        )
      })}
    </ul>
  )
}

export default AnswersList;