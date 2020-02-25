import { TestBed } from '@angular/core/testing';

import { LanapiService } from './lanapi.service';

describe('LanapiService', () => {
  let service: LanapiService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(LanapiService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
