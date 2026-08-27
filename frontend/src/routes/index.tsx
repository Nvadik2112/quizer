
import { createBrowserRouter } from 'react-router-dom';

import Layout from '@/layouts/Layout.tsx';
import React from "react";
import Home from '@/pages/Home/Home.tsx';
import Quiz from "@/pages/Quiz/Quiz.tsx";
import Auth from "@/pages/Auth/Auth.tsx";
// import { useAuthStore } from "@/store/authStore.ts";
// import About from '@/pages/About';
// import Todos from '@/pages/Todos';
// import TodoDetail from '@/pages/TodoDetail';
// import Login from '@/pages/Login';
// import NotFound from '@/pages/NotFound';

// Проверка авторизации
// const { isAuthenticated } = useAuthStore();

// @ts-ignore
const ProtectedRoute = ({ children }: { children: React.ReactNode }) => {
  // if (!isAuthenticated) {
  //  return <Navigate to="/" replace />;
  // }
  // return children;
};

export const router = createBrowserRouter([
  {
    path: '/',
    element: (
      //<ProtectedRoute>
        <Layout />
      //</ProtectedRoute>
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
      {
        path: 'auth',
        element: <Auth />
      }
    ],
  },
]);

export default router;