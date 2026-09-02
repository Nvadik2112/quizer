import QuizForm from "@/components/QuizForm/QuizForm.tsx";
import './QuizCreator.css'

const quizCreator = () => {
  return (
    <div className='QuizCreator'>
      <QuizForm title='Создать тест' />
    </div>
  )
}

export default quizCreator;