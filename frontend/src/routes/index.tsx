
import { createBrowserRouter } from 'react-router-dom';

// Компоненты
import Layout from '@/layouts/Layout.tsx';
import React from "react";
import Home from '@/pages/Home/Home.tsx';
import Quiz from "@/pages/Quiz/Quiz.tsx";
// import About from '@/pages/About';
// import Todos from '@/pages/Todos';
// import TodoDetail from '@/pages/TodoDetail';
// import Login from '@/pages/Login';
// import NotFound from '@/pages/NotFound';

// Проверка авторизации
//const isAuthenticated = (): boolean => {
//  return !!localStorage.getItem('token');
//};

// Компонент для защищенных маршрутов
const ProtectedRoute = ({ children }: { children: React.ReactNode }) => {
  //if (!isAuthenticated()) {
  //  return <Navigate to="/login" replace />;
  //}
  return children;
};

// Создаем роутер
export const router = createBrowserRouter([
  {
    path: '/',
    element: (
      <ProtectedRoute>
        <Layout />
      </ProtectedRoute>
    ),
    children: [
      {
        index: true,
        element: <Home />,
      },
      {
        path: 'quiz/:id',
        element: <Quiz />,
      },
    ],
  },
]);

export default router;