export class Errors {
  success: boolean;
  status: number;
  message: {
    [field: string]: string[]
  };
}
