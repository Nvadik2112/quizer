import './AnswersList.css'
import AnswerItem from "@/components/ActiveQuiz/AnswersList/AnswerItem/AnswerItem.tsx";

// @ts-ignore
const AnswersList = (props) => (
  <ul className="AnswersList">
    { props.answers.map((answer: any, index: any) => {
      return (
        <AnswerItem
          key={index}
          index={index}
          answer={answer}
          onAnswerClick={props.onAnswerClick}
          status={ props.questionAnswer.answerIndex == index ? props.questionAnswer.status : null }
        />
      )
    })}
  </ul>
)

export default AnswersList