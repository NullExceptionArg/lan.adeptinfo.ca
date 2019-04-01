import {NgModule} from '@angular/core';
import {SharedModule} from '../shared/shared.module';
import {LandingComponent} from './landing.component';
import {LandingRoutingModule} from './landing-routing.module';
import {CreateLanComponent} from '../create-lan/create-lan.component';
import {SharedLanModule} from '../shared/lan/shared-lan.module';

@NgModule({
  imports: [
    SharedModule,
    LandingRoutingModule,
    SharedLanModule
  ],
  declarations: [
    LandingComponent
  ], entryComponents: [
    CreateLanComponent
  ]
})
export class LandingModule {
}
