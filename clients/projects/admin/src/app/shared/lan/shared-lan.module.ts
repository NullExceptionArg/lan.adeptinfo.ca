import {NgModule} from '@angular/core';
import {SharedModule} from '../shared.module';
import {CreateLanDetailsComponent} from './details/create-lan-details.component';
import {CreateLanSeatsComponent} from './seats/create-lan-seats.component';
import {SeatsioAngularModule} from '@seatsio/seatsio-angular';
import {OwlModule} from 'ngx-owl-carousel';
import {CreateLanCoordinatesComponent} from './coordinates/create-lan-coordinates.component';
import {AgmCoreModule} from '@agm/core';
import {environment} from '../../../environments/environment';
import {CreateLanRulesComponent} from './rules/create-lan-rules.component';
import {CreateLanDescriptionComponent} from './description/create-lan-description.component';
import {CovalentTextEditorModule} from '@covalent/text-editor';

@NgModule({
  imports: [
    SharedModule,
    SeatsioAngularModule,
    OwlModule,
    AgmCoreModule.forRoot({
      apiKey: environment.googleMapsApiKey,
      libraries: ['places']
    }),
    CovalentTextEditorModule
  ],
  declarations: [
    CreateLanDetailsComponent,
    CreateLanSeatsComponent,
    CreateLanCoordinatesComponent,
    CreateLanRulesComponent,
    CreateLanDescriptionComponent
  ],
  exports: [
    CreateLanDetailsComponent,
    CreateLanSeatsComponent,
    CreateLanCoordinatesComponent,
    CreateLanRulesComponent,
    CreateLanDescriptionComponent
  ]
})
export class SharedLanModule {
}
