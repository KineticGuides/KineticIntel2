import { ResolveFn } from '@angular/router';

export const ddResolver: ResolveFn<boolean> = (route, state) => {
  return true;
};
