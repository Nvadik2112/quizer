import apiClient from "@/api/client.ts";

export const authApi = {
  signin: async (email: string, password: string): Promise<any> => {
    const response = await apiClient.post<any>('/signin',
      {
        email,
        password,
      }
   );

    return response.data;
  },
  signup: async (email: string, password: string): Promise<any> => {
    const response = await apiClient.post<any>('/signup',
      {
        email,
        password,
      }
    );

    return response.data;
  }
}