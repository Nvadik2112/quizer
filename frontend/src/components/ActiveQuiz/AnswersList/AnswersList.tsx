import './AnswersList.css'
import AnswerItem from "@/components/ActiveQuiz/AnswersList/AnswerItem/AnswerItem.tsx";
import { useQuizStore } from "@/store";

const AnswersList = () => {
  const {
    getCurrentQuestion,
  } = useQuizStore();

  const { answers } = getCurrentQuestion();

  return (
    <ul className="AnswersList">
      {answers.map((answer: any, index: any) => {
        return (
          <AnswerItem
            key={index}
            index={index}
            answer={answer}
          />
        )
      })}
    </ul>
  )
}

export default AnswersList;