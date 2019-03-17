import {NgModule} from '@angular/core';
import {SharedModule} from '../../shared/shared.module';
import {CreateLanComponent} from './create-lan.component';
import {CreateLanDetailsComponent} from './details/create-lan-details.component';
import {CreateLanSeatsComponent} from './seats/create-lan-seats.component';
import {SeatsioAngularModule} from '@seatsio/seatsio-angular';
import {OwlModule} from 'ngx-owl-carousel';

@NgModule({
  imports: [
    SharedModule,
    SeatsioAngularModule,
    OwlModule
  ],
  declarations: [
    CreateLanComponent,
    CreateLanDetailsComponent,
    CreateLanSeatsComponent
  ]
})
export class CreateLanModule {
}
