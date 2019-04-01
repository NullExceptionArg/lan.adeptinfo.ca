import {NgModule} from '@angular/core';
import {SharedModule} from '../shared/shared.module';
import {LanRoutingModule} from './lan-routing.module';
import {CreateLanComponent} from '../create-lan/create-lan.component';
import {SharedLanModule} from '../shared/lan/shared-lan.module';
import {LanComponent} from './lan.component';

@NgModule({
  imports: [
    SharedModule,
    LanRoutingModule,
    SharedLanModule
  ],
  declarations: [
    LanComponent
  ], entryComponents: [
    CreateLanComponent
  ]
})
export class LanModule {
}
