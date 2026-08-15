import './AnswerItem.css'
import { useQuizStore } from "@/store";

const AnswerItem = (props: any) => {
  const classes =["AnswerItem"];

  const {
    getCurrentQuestion,
    getCurrentQuestionAnswer,
    quizAnswerClick
  } = useQuizStore();

  const { answerIndex, status } = getCurrentQuestionAnswer();

  if (answerIndex == props.index) {
    classes.push(`AnswerItem--${status}`);
  }

  const { id } = getCurrentQuestion();

  return (
    <li className={classes.join(' ')}
        onClick={() => quizAnswerClick(id, props.index)}>
      { props.answer }
    </li>
  )
}

export default AnswerItem;