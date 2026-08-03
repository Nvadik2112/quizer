import Button from "@/components/UI/Button/Button.tsx";
import { Link } from "react-router-dom";
import './FinishedQuiz.css'

const FinishedQuiz = (props: any) => {
  const successCount = Object.keys(props.results).reduce(
    (total, _obj, index) => {
      if (props.results[index].status === 'success') {
        total++
      }
      return total
    }, 0
  )

  return (
    <div className="FinishedQuiz">
      <ul>
        {props.questions.map((item: any, index: any) => {
          const cls = [
            'fa',
            props.results[index].status === 'error' ? 'fa-times' : 'fa-check',
            `FinishedQuiz--${props.results[index].status}`,
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
      <p>Правильно {successCount} из {props.questions.length}</p>
      <div>
        <Button onClick={props.onRetry} type='primary'>Повторить</Button>
        <Link to='/'>
          <Button type='success'>Перейти в список тестов</Button>
        </Link>
      </div>
    </div>
  )
}

export default FinishedQuiz;