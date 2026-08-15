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

  const successCount = Object.keys(questionAnswers).reduce(
    (total, _obj, index) => {
      if (questionAnswers[index].status === 'success') {
        total++
      }
      return total
    }, 0
  )

  const quizLength = questions.length;

  return (
    <div className="FinishedQuiz">
      <ul>
        {questions.map((item: any, index: any) => {
          const cls = [
            'fa',
            questionAnswers[index].status === 'error' ? 'fa-times' : 'fa-check',
            `FinishedQuiz--${questionAnswers[index].status}`,
          ]

          return (
            <li
              key={index}
            >
              <strong>{index + 1}</strong> .&nbsp;
              {item.title}
              <i className={cls.join(' ')} />
            </li>
          )
        })}
      </ul>
      <p>Правильно {successCount} из {quizLength}</p>
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