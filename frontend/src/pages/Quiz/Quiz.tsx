import './Quiz.css';
import { useParams } from "react-router-dom";
import { useEffect } from "react";
import ActiveQuiz from "@/components/ActiveQuiz/ActiveQuiz.tsx";
import Button from "@/components/UI/Button/Button.tsx";
import FinishedQuiz from "@/components/FinishedQuiz/FinishedQuiz.tsx";
import { useQuizStore } from "@/store";
import Loader from "@/components/UI/Loader/Loader.tsx";
const Quiz = () => {
  const { id } = useParams<{ id: string }>();

  const {
    isLoading,
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
    if (id) {
      void loadQuestions(id);
    }

    return () => {
      setTestDefault();
    };
  }, []);

  const currentQuestion = getCurrentQuestion();
  const currentQuestionAnswer = getCurrentQuestionAnswer();

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