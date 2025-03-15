import { TestBed } from '@angular/core/testing';
import { ResolveFn } from '@angular/router';

import { ddResolver } from './dd.resolver';

describe('ddResolver', () => {
  const executeResolver: ResolveFn<boolean> = (...resolverParameters) => 
      TestBed.runInInjectionContext(() => ddResolver(...resolverParameters));

  beforeEach(() => {
    TestBed.configureTestingModule({});
  });

  it('should be created', () => {
    expect(executeResolver).toBeTruthy();
  });
});
