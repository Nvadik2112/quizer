import Button from "@/components/UI/Button/Button.tsx";
import { Link } from "react-router-dom";
import './FinishedQuiz.css'
import { useQuizStore } from "@/store";

const FinishedQuiz = () => {
  const {
    questions,
    questionAnswers,
    setTestDefault
  } = useQuizStore();

  const successCount = questionAnswers
    ? Object.values(questionAnswers).filter(a => a.status === 'success').length
    : null;

  const quizLength = questions.length;

  return (
    <div className="FinishedQuiz">
      <ul>
        {questions.map((item, index) => {
          const cls = [
            'fa',
            questionAnswers && questionAnswers[index].status === 'success' ? 'fa-check' : 'fa-times',
            `FinishedQuiz--${questionAnswers ? questionAnswers[index].status : 'error'}`,
          ]

          return (
            <li key={index}>
              <strong>{index + 1}</strong> .&nbsp;
              {item.title}
              <i className={cls.join(' ')} />
            </li>
          )
        })}
      </ul>
      {
        successCount !== null && <p>Правильно {successCount} из {quizLength}</p>
      }
      <div>
        <Button type='primary' onClick={setTestDefault}>Повторить</Button>
        <Link to='/'>
          <Button type='success'>Перейти в список тестов</Button>
        </Link>
      </div>
    </div>
  )
}

export default FinishedQuiz;