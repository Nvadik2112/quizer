
import {createBrowserRouter, Navigate} from 'react-router-dom';

import Layout from '@/layouts/Layout.tsx';
import React from "react";
import Home from '@/pages/Home/Home.tsx';
import Quiz from "@/pages/Quiz/Quiz.tsx";
import Auth from "@/pages/Auth/Auth.tsx";
import { useAuthStore } from "@/store/authStore.ts";
import QuizCreator from "@/pages/QuizCreator/QuizCreator.tsx";

const ProtectedRoute = ({ children }: { children: React.ReactNode }) => {
  const { isAuthenticated } = useAuthStore();

  if (!isAuthenticated) {
    return <Navigate to="/" replace />;
  }

  return children;
};

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
      {
        path: 'auth',
        element: <Auth />
      },
      {
        path: 'quiz-creator',
        element: <QuizCreator />
      }
    ],
  },
]);

export default router;