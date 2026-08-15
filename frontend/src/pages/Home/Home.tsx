import './Home.css';
import { NavLink } from 'react-router-dom';
import { useEffect } from 'react';
import { useQuizStore } from "@/store";

const Home = () => {
  const {
    tests,
    loadTests
  } = useQuizStore();

  useEffect(() => {
    loadTests();
  }, []);

  return (
    <div className="QuizList">
      <div>
        <h1>Список тестов</h1>
        <ul>
          {tests.map((test) => (
            <li key={test.id}>
              <NavLink to={`/quiz/${test.id}`}>
                {test.title}
              </NavLink>
            </li>
          ))}
        </ul>
      </div>
    </div>
  );
};

export default Home;