import './Quiz.css';
import { useParams } from "react-router-dom";
import { useEffect, useMemo, useState} from "react";
import ActiveQuiz from "@/components/ActiveQuiz/ActiveQuiz.tsx";
import Button from "@/components/UI/Button/Button.tsx";
import FinishedQuiz from "@/components/FinishedQuiz/FinishedQuiz.tsx";
const Quiz = () => {
  const { id } = useParams<{ id: string }>();

  const [questions, setQuestions] = useState<any[]>([]);
  const [questionAnswers, setState] = useState();

  const setDefaultAnswers = (data: any) => {
    const initialAnswers = Object.fromEntries(
      data.map((_question: any, index: any) => [
        index,
        {
          answerIndex: null,
          status: ''
        }
      ])
    );

    // @ts-ignore
    setState(initialAnswers);
  }

  useEffect(() => {
    fetch(`http://localhost:8000/questions?testId=${id}`)
      .then(res => res.json())
      .then(data => {
        setQuestions(data);
        setDefaultAnswers(data);
      })
      .catch(err => {
        console.error('❌ Ошибка:', err);
        // setLoading(false);
      });


  }, []);

  const [activeIndex, setActiveIndex] = useState(0);

  const currentQuestion = useMemo((): any => {
    return questions[activeIndex]
  }, [questions, activeIndex]);

  const currentQuestionAnswer = useMemo((): any => {
    if (questionAnswers) {
      return questionAnswers[activeIndex];
    }

    return null;

  }, [questionAnswers, activeIndex]);


  const quizAnswerClick = (questionId: number, answerIndex: number) => {
    if (currentQuestionAnswer?.answerIndex === null) {
      fetch(`http://localhost:8000/questions/${questionId}/check`, {
        method: 'POST',
        body: JSON.stringify({
          correctAnswerIndex: answerIndex
        }),
      }).then(res => res.json())
        .then(data => {
          setState((prevState: any) => {
            return ({
              ...prevState,
              [activeIndex]: {
                answerIndex,
                status: data ? 'success' : 'error'
              }
            });
          });
        });
    }
  };

  const buttonTitle = useMemo((): any => {
    if (activeIndex === (questions.length - 1)) {
      return 'Завершение теста';
    }

    return 'Следующий вопрос';

  }, [activeIndex])

  const [isFinished, setIsFinished] = useState(false);

  const nextQuestion = () => {
    setActiveIndex(activeIndex + 1);

    if (activeIndex === (questions.length - 1)) {
      setIsFinished(true);
    }
  }

  const retryTest = () => {
    setActiveIndex(0);
    setIsFinished(false);
    setDefaultAnswers(questions);
  }

  return (
    <div className="Quiz">
      <div className="Quiz__wrap">
        <h1>Пожалуйста ответьте на вопросы</h1>
        {
          currentQuestion && !isFinished && (
            <ActiveQuiz
              answers={currentQuestion.answers}
              question={currentQuestion.title}
              onAnswerClick={(answerIndex: number) => quizAnswerClick(currentQuestion.id, answerIndex)}
              quizLength={questions.length}
              answerNumber={activeIndex + 1}
              questionAnswer={currentQuestionAnswer}
            />
          )
        }
        {
          isFinished && (
            <FinishedQuiz
              results={questionAnswers}
              questions={questions}
              onRetry={retryTest}
            />
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