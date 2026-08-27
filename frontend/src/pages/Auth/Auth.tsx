import './Auth.css'
import Button from "@/components/UI/Button/Button.tsx";
import Input from "@/components/UI/Input/Input.tsx"
import Loader from "@/components/UI/Loader/Loader.tsx";
import { useNavigate } from 'react-router-dom';
import { useAuthStore } from "@/store/authStore.ts";
import { type SyntheticEvent, useState } from "react";
import { useSignin, useSignup } from "@/hooks/useAuth.ts";
import { AxiosError } from "axios";

const Auth = () => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [emailError, setEmailError] = useState('');
  const [passwordError, setPasswordError] = useState('');
  const validateEmail = (value: string): boolean => {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
  };
  const validatePassword = (value: string): boolean => {
    return  value.length >= 6;
  }

  const isFormValid = validateEmail(email) && validatePassword(password);
  const [requestError, setRequestError] = useState('');
  const [isSubmit, setIsSubmit] = useState(false);

  const setEmailErrorMessage = (value: string) => {
    setEmailError(value && !validateEmail(value) ? 'Введите корректный email' : '');
  }

  const handleEmailChange = (value: string) => {
    setEmail(value);

    if (isSubmit) {
      setEmailErrorMessage(value);
    }
  };

  const setPasswordErrorMessage = (value: string) => {
    setPasswordError(value && !validatePassword(value) ? 'Минимум 6 символов' : '');
  }

  const handlePasswordChange = (value: string) => {
    setPassword(value);

    if (isSubmit) {
      setPasswordErrorMessage(value)
    }
  };

  const submitHandler = (event: SyntheticEvent<HTMLFormElement>) => {
    event.preventDefault();

    if (requestError) {
      setRequestError('');
    }

    setIsSubmit(true);
    setEmailErrorMessage(email);
    setPasswordErrorMessage(password)
  }

  const { setAuth } = useAuthStore();

  const {
    mutate: singin,
    isPending: loginPending
  } = useSignin();

  const setErrorMessage = (error: unknown) => {
    if (error instanceof AxiosError) {
      const message = error.response?.data?.error || 'Ошибка входа';
      setRequestError(message);
    } else {
      setRequestError('Ошибка входа');
    }
  }

  const navigate = useNavigate();

  const loginHandler = () => {
    singin(
      { email, password },
      {
        onSuccess: (data) => {
          setAuth(data.access_token, email);
          navigate('/');
        },
        onError: (error: unknown) => {
          setErrorMessage(error);
        }
      }
    )
  }

  const {
    mutate: singup,
    isPending: registerPending
  } = useSignup();

  const registerHandler = () => {
    if (requestError) {
      setRequestError('');
    }

    singup(
      { email, password },
      {
        onSuccess: () => {
          loginHandler();
        },
        onError: (error: unknown) => {
          setErrorMessage(error);
        }
      }
    )
  }

  return (
    <div className='Auth'>
      <div>
        <h1>Авторизация</h1>
        <form className='Auth__form' onSubmit={submitHandler}>
          <Input
            type='email'
            value={email}
            label='Email'
            invalid={!!emailError}
            errorMessage={emailError}
            onChange={handleEmailChange}
          />
          <Input
            type='password'
            value={password}
            label='Пароль'
            invalid={!!passwordError}
            errorMessage={passwordError}
            onChange={handlePasswordChange}
          />
          <div className='Auth__status'>
            {registerPending || loginPending ? <Loader /> : requestError}
          </div>
          <div className="Auth__form_buttons">
            <Button type='success'
                    onClick={loginHandler}
                    disabled={!isFormValid}
            >
              Войти
            </Button>
            <Button type='primary'
                    onClick={registerHandler}
                    disabled={!isFormValid}
            >
              Регистрация
            </Button>
          </div>
        </form>
      </div>
    </div>
  )
}

export default Auth;
