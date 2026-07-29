import './Quiz.css';
import { useParams } from "react-router-dom";
import { useEffect, useMemo, useState} from "react";
import ActiveQuiz from "@/components/ActiveQuiz/ActiveQuiz.tsx";
const Quiz = () => {
  const { id } = useParams<{ id: string }>();

  const [questions, setQuestions] = useState<any[]>([]);
  const [questionAnswers, setState] = useState();

  useEffect(() => {
    fetch(`http://localhost:8000/questions?testId=${id}`)
      .then(res => res.json())
      .then(data => {
        setQuestions(data);

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
      })
      .catch(err => {
        console.error('❌ Ошибка:', err);
        // setLoading(false);
      });


  }, []);

  const [activeIndex] = useState(0)

  const currentQuestion = useMemo((): any => {
    return questions[activeIndex]
  }, [questions]);

  const currentQuestionAnswer = useMemo((): any => {
    if (questionAnswers) {
      return questionAnswers[activeIndex];
    }

    return null;

  }, [questionAnswers]);


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

  return (
    <div className="Quiz">
      <div className="QuizWrapper">
        <h1>Пожалуйста ответьте на вопросы</h1>
        {
          currentQuestion && (
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
      </div>
    </div>
  )
};

export default Quiz;