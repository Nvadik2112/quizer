import './Home.css';
import { NavLink } from 'react-router-dom';
import { useEffect } from 'react';
import { useQuizStore } from "@/store";
import Loader from "@/components/UI/Loader/Loader.tsx";
import { useTests } from "@/hooks/useQuiz.ts";

const Home = () => {
  const {
    data,
    isLoading,
  } = useTests();

  const {
    tests,
    setTests,
  } = useQuizStore();

  useEffect(() => {
    if (data?.length) {
      setTests(data);
    }
  }, [data]);

  const renderTests = () => {
    if (isLoading) {
      return <Loader />
    }

    const mappedTests = tests.map((test) => (
      <li key={test.id}>
        <NavLink to={`/quiz/${test.id}`}>
          {test.title}
        </NavLink>
      </li>)
    );

    return <ul>
      {mappedTests}
    </ul>
  };

  return (
    <div className="QuizList">
      <div>
        <h1>Список тестов</h1>
        {renderTests()}
      </div>
    </div>
  );
};

export default Home;