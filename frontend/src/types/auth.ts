export interface AuthState {
  accessToken: string | null;
  user: string | null;
  isAuthenticated: boolean;
  setAuth: (accessToken: string, user: string) => void;
  logout: () => void;
}