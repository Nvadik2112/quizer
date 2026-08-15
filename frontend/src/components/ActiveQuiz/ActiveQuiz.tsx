import AnswersList from "@/components/ActiveQuiz/AnswersList/AnswersList.tsx";
import './ActiveQuiz.css'
import { useQuizStore } from "@/store";

const ActiveQuiz = () => {
  const {
    questions,
    activeIndex,
    getCurrentQuestion,
  } = useQuizStore();

  const { title } = getCurrentQuestion();
  const answerNumber = activeIndex + 1;
  const quizLength = questions.length;

  return (
    <div className="ActiveQuiz">
      <p className="Question">
        <span>
          <strong>{answerNumber}</strong>&nbsp;
          {title}
        </span>
        <small>{answerNumber} из {quizLength}</small>
      </p>
      <AnswersList />
    </div>
  )
}

export default ActiveQuiz;