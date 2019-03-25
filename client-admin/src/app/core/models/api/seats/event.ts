export interface Event {
  bookWholeTables: boolean;
  chartKey: string;
  createdOn: {
    date: string,
    timezone: string,
    timezone_type: string
  };
  forSaleConfig: string;
  id: number;
  key: string;
  supportsBestAvailable: string;
  tableBookingModes: string;
  updatedOn: {
    date: string,
    timezone: string,
    timezone_type: string
  };
}
