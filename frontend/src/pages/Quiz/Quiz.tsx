import './Quiz.css';
import { useParams } from "react-router-dom";
import { useEffect } from "react";
import ActiveQuiz from "@/components/ActiveQuiz/ActiveQuiz.tsx";
import Button from "@/components/UI/Button/Button.tsx";
import FinishedQuiz from "@/components/FinishedQuiz/FinishedQuiz.tsx";
import { useQuizStore } from "@/store";
const Quiz = () => {
  const { id } = useParams<{ id: string }>();

  const {
    questions,
    activeIndex,
    isFinished,
    getCurrentQuestion,
    getCurrentQuestionAnswer,
    loadQuestions,
    nextQuestion,
    setTestDefault
  } = useQuizStore();

  useEffect(() => {
    loadQuestions(id);

    return () => {
      setTestDefault();
    };
  }, []);

  const currentQuestion = getCurrentQuestion();
  const currentQuestionAnswer = getCurrentQuestionAnswer();

  const buttonTitle = activeIndex === questions.length - 1
    ? 'Завершение теста'
    : 'Следующий вопрос';

  return (
    <div className="Quiz">
      <div className="Quiz__wrap">
        <h1>Пожалуйста ответьте на вопросы</h1>
        {
          currentQuestion && !isFinished && (
            <ActiveQuiz />
          )
        }
        {
          isFinished && (
            <FinishedQuiz />
          )
        }
        {
          currentQuestionAnswer?.answerIndex !== null &&
          !isFinished &&
            <div className="Quiz__button">
              <Button
                children={buttonTitle}
                onClick={nextQuestion}
              />
            </div>
        }
      </div>
    </div>
  )
};

export default Quiz;