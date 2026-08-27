import { useMutation } from "@tanstack/react-query";
import { authApi } from "@/api/auth.ts";

export const useSignin = () => {
  return useMutation({
    mutationFn: ({ email, password }: { email: string, password: string }) =>
      authApi.signin(email, password),
  });
}

export const useSignup = () => {
  return useMutation({
    mutationFn: ({ email, password }: { email: string, password: string }) =>
      authApi.signup(email, password),
  });
}