import { NavLink } from 'react-router-dom';
import { useEffect, useState } from 'react';
import './Home.css';

// 👇 Тип для данных с сервера
interface Quiz {
  id: number;
  title: string;
}

const Home = () => {
  // 👇 Состояние для хранения данных с сервера
  const [quizes, setQuizes] = useState<Quiz[]>([]);
  // const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch('http://localhost:8000/tests')
      .then(res => res.json())
      .then(data => {
        console.log('✅ Данные с сервера:', data);
        // 👇 Записываем данные в состояние
        setQuizes(data);
        setLoading(false);
      })
      .catch(err => {
        console.error('❌ Ошибка:', err);
        // setLoading(false);
      });
  }, []);

  return (
    <div className="QuizList">
      <div>
        <h1>Список тестов</h1>
        <ul>
          {quizes.map((quiz) => (
            <li key={quiz.id}>
              <NavLink to={`/quiz/${quiz.id}`}>
                {quiz.title}
              </NavLink>
            </li>
          ))}
        </ul>
      </div>
    </div>
  );
};

export default Home;