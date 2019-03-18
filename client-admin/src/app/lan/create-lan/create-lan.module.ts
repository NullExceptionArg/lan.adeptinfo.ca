import {NgModule} from '@angular/core';
import {SharedModule} from '../../shared/shared.module';
import {CreateLanComponent} from './create-lan.component';
import {CreateLanDetailsComponent} from './details/create-lan-details.component';
import {CreateLanSeatsComponent} from './seats/create-lan-seats.component';
import {SeatsioAngularModule} from '@seatsio/seatsio-angular';
import {OwlModule} from 'ngx-owl-carousel';
import {CreateLanCoordinatesComponent} from './coordinates/create-lan-coordinates.component';
import {AgmCoreModule} from '@agm/core';
import {environment} from '../../../environments/environment';

@NgModule({
  imports: [
    SharedModule,
    SeatsioAngularModule,
    OwlModule,
    AgmCoreModule.forRoot({
      apiKey: environment.googleMapsApiKey,
      libraries: ['places']
    })
  ],
  declarations: [
    CreateLanComponent,
    CreateLanDetailsComponent,
    CreateLanSeatsComponent,
    CreateLanCoordinatesComponent
  ]
})
export class CreateLanModule {
}
