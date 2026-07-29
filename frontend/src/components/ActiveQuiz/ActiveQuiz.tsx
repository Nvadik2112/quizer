import AnswersList from "@/components/ActiveQuiz/AnswersList/AnswersList.tsx";
import './ActiveQuiz.css'

// @ts-ignore
const ActiveQuiz = (props) => (
  <div className="ActiveQuiz">
    <p className="Question">
      <span>
        <strong>{props.answerNumber}</strong>&nbsp;
              {props.question}
      </span>
      <small>{props.answerNumber} из {props.quizLength}</small>
    </p>
    <AnswersList
      questionAnswer={props.questionAnswer}
      answers={props.answers}
      onAnswerClick={props.onAnswerClick}
    />
  </div>
)

export default ActiveQuiz;