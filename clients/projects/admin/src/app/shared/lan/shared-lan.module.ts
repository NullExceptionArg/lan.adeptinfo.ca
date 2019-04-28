import {NgModule} from '@angular/core';
import {SharedModule} from '../shared.module';
import {LanDetailsComponent} from './details/lan-details.component';
import {LanSeatsComponent} from './seats/lan-seats.component';
import {SeatsioAngularModule} from '@seatsio/seatsio-angular';
import {OwlModule} from 'ngx-owl-carousel';
import {LanCoordinatesComponent} from './coordinates/lan-coordinates.component';
import {AgmCoreModule} from '@agm/core';
import {environment} from '../../../environments/environment';
import {LanRulesComponent} from './rules/lan-rules.component';
import {LanDescriptionComponent} from './description/lan-description.component';
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
    LanDetailsComponent,
    LanSeatsComponent,
    LanCoordinatesComponent,
    LanRulesComponent,
    LanDescriptionComponent
  ],
  exports: [
    LanDetailsComponent,
    LanSeatsComponent,
    LanCoordinatesComponent,
    LanRulesComponent,
    LanDescriptionComponent
  ]
})
export class SharedLanModule {
}
