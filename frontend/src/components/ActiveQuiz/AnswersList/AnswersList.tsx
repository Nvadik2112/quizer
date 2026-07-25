import './AnswersList.css'
import AnswerItem from "@/components/ActiveQuiz/AnswersList/AnswerItem/AnswerItem.tsx";

// @ts-ignore
const AnswersList = (props) => (
  <ul className="AnswersList">
    { props.answers.map((answer: any, index: any) => {
      return (
        <AnswerItem
          key={index}
          answer={answer}
          onAnswerClick={props.onAnswerClick}
          state={ props.state ? props.state[answer.id] : null }
        />
      )
    })}
  </ul>
)

export default AnswersList