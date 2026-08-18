import './Quiz.css';
import { useParams } from "react-router-dom";
import { useQuestions } from "@/hooks/useQuiz.ts";
import { useEffect } from "react";
import ActiveQuiz from "@/components/ActiveQuiz/ActiveQuiz.tsx";
import Button from "@/components/UI/Button/Button.tsx";
import FinishedQuiz from "@/components/FinishedQuiz/FinishedQuiz.tsx";
import { useQuizStore } from "@/store";
import Loader from "@/components/UI/Loader/Loader.tsx";

const Quiz = () => {
  const { id } = useParams<{ id: string }>();
  const testId = id || '';

  const {
    data,
    isLoading,
  } = useQuestions(testId);

  const {
    questions,
    activeIndex,
    isFinished,
    getCurrentQuestion,
    getCurrentAnswerStatus,
    nextQuestion,
    setTestDefault,
    setDefaultAnswers,
    setQuestions
  } = useQuizStore();

  useEffect(() => {
    if (data?.length) {
      setQuestions(data);
      setDefaultAnswers(data);
    }

    return () => {
      setTestDefault();
    };
  }, [data]);

  const currentQuestion = getCurrentQuestion();
  const currentAnswerStatus = getCurrentAnswerStatus();

  const buttonTitle = activeIndex === questions.length - 1
    ? 'Завершение теста'
    : 'Следующий вопрос';

  const renderContent = () => {
    if (isLoading) {
      return <Loader />;
    }

    if (currentQuestion && !isFinished) {
      return <ActiveQuiz />;
    }

    return <FinishedQuiz />;
  };

  return (
    <div className="Quiz">
      <div className="Quiz__wrap">
        <h1>Пожалуйста ответьте на вопросы</h1>
        {renderContent()}
        {
          currentAnswerStatus?.answerIndex !== null &&
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