import {NgModule} from '@angular/core';
import {SharedModule} from '../shared/shared.module';
import {LandingComponent} from './landing.component';
import {LandingRoutingModule} from './landing-routing.module';
import {CreateLanComponent} from '../lan/create-lan/create-lan.component';
import {CreateLanModule} from '../lan/create-lan/create-lan.module';

@NgModule({
  imports: [
    SharedModule,
    LandingRoutingModule,
    CreateLanModule
  ],
  declarations: [
    LandingComponent
  ], entryComponents: [
    CreateLanComponent
  ]
})
export class LandingModule {
}
