import './Quiz.css';
import { useParams } from "react-router-dom";
import {useEffect, useMemo, useRef, useState} from "react";
import ActiveQuiz from "@/components/ActiveQuiz/ActiveQuiz.tsx";
const Quiz = () => {
  const { id } = useParams<{ id: string }>();

  const [questions, setQuestions] = useState<any[]>([]);

  useEffect(() => {
    fetch(`http://localhost:8000/questions?testId=${id}`)
      .then(res => res.json())
      .then(data => {
        setQuestions(data);
        console.log('✅ Данные с сервера:', questions);
      })
      .catch(err => {
        console.error('❌ Ошибка:', err);
        // setLoading(false);
      });
  }, []);

  const activeIndex = useRef(0)

  const currentQuestion = useMemo(() => {
    return questions[activeIndex.current]
  }, [questions]);

  console.log((currentQuestion));

  // @ts-ignore
  return (
    <div className="Quiz">
      <div className="QuizWrapper">
        <h1>Пожалуйста ответьте на вопросы</h1>
        {
          currentQuestion && (
            <ActiveQuiz
              answers={currentQuestion.answers}
              question={currentQuestion.title}
              // onAnswerClick={this.props.quizAnswerClick}
              quizLength={questions.length}
              answerNumber={activeIndex.current + 1}
              state='error'
            />
          )
        }

      </div>
    </div>
  )
};

export default Quiz;