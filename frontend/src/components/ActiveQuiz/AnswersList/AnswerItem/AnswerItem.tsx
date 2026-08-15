import './AnswerItem.css'
import { useQuizStore } from "@/store";
import type {AnswerIndex} from "@/types/quiz.ts";

interface AnswerItemProps {
  index: AnswerIndex,
  answer: string,
}

const AnswerItem = (props: AnswerItemProps) => {
  const classes =["AnswerItem"];

  const {
    getCurrentQuestion,
    getCurrentQuestionAnswer,
    quizAnswerClick
  } = useQuizStore();

  const currentQuestionAnswer = getCurrentQuestionAnswer();
  const answerIndex = currentQuestionAnswer?.answerIndex ?? null;
  const status = currentQuestionAnswer?.status ?? '';

  if (answerIndex !== null && answerIndex == props.index) {
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