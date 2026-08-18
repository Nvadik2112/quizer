import './AnswerItem.css'
import { useQuizStore } from "@/store";
import type {AnswerIndex} from "@/types/quiz.ts";

interface AnswerItemProps {
  index: AnswerIndex,
  answer: string,
  isPending: boolean,
  handleAnswer: () => void
}

const AnswerItem = (props: AnswerItemProps) => {
  const classes =["AnswerItem"];

  const {
    getCurrentAnswerStatus,
  } = useQuizStore();

  const currentAnswerStatus = getCurrentAnswerStatus();
  const answerIndex = currentAnswerStatus?.answerIndex ?? null;
  const status = currentAnswerStatus?.status ?? '';

  if (status || props.isPending) {
    classes.push(`AnswerItem--disabled`);
  }

  if (props.isPending) {
    classes.push(`AnswerItem--pending`);
  }

  if (answerIndex !== null && answerIndex == props.index) {
    classes.push(`AnswerItem--${status}`);
  }

  return (
    <li className={classes.join(' ')}
        onClick={props.handleAnswer}>
      { props.answer }
    </li>
  )
}

export default AnswerItem;