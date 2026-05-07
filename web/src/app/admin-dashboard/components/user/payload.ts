import { User } from './user';

export type Payload = Partial<User> & {
  password?: string;
};
