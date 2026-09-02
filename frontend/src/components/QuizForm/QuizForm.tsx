import Input from "@/components/UI/Input/Input.tsx";
import { useState } from "react";
import Select from "@/components/UI/Select/Select.tsx";
import './QuizForm.css';
import Button from "@/components/UI/Button/Button.tsx";

const QuizForm = (props: any) => {
  const [question, setQuestion] = useState('');
  const [answers, setAnswers] = useState([
    { value: '' },
    { value: '' },
    { value: '' },
    { value: '' }
  ]);
  const [currentAnswer, setCurrentAnswer] = useState(0)

  const handleAnswerChange = (index: number, value: string) => {
    setAnswers((prev) =>
      prev.map((item, i) => i === index ? { value } : item)
    );
  };

  const renderAnswerInput = () => {
    return answers.map((answer, index) => {
      return (
        <Input label={`Вариант ${index + 1}`}
               value={answer.value}
               onChange={(value) => handleAnswerChange(index, value)}
        />
      )
    })
  };

  const answerOptions = answers.map(
    (_, index) => (
      { value: index, title: String(index + 1) }
    )
  );

  const handleCurrentAnswerChange = (value: string) => {
    setCurrentAnswer(Number(value))
  };

  return (
    <div className='QuizForm'>
      <h1>{props.title}</h1>
      <form>
        <Input
          type='text'
          value={question}
          label='Введите вопрос'
          onChange={setQuestion}
        />
        <div>
          {renderAnswerInput()}
        </div>
        <Select
          value={currentAnswer}
          label={'Выберите правильный ответ'}
          options={answerOptions}
          onChange={handleCurrentAnswerChange}
        />
        <div className='QuizForm__buttons'>
          <div className='QuizForm__nav_buttons'>
            <Button onClick={() => null}
                    disabled={false}
            >
              Предыдущий вопрос
            </Button>
            <Button onClick={() => null}
                    disabled={false}
            >
              Следующий вопрос
            </Button>
          </div>
          <div className='QuizForm__action_buttons'>
            <Button type='success'
                    disabled={false}
            >
              Создать тест
            </Button>
          </div>
        </div>
      </form>
    </div>
  )
}

export default QuizForm;