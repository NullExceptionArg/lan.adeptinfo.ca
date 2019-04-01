import {NgModule} from '@angular/core';
import {SharedModule} from '../shared/shared.module';
import {CreateLanComponent} from './create-lan.component';
import {SharedLanModule} from '../shared/lan/shared-lan.module';

@NgModule({
  imports: [
    SharedModule,
    SharedLanModule
  ],
  declarations: [
    CreateLanComponent,
  ]
})
export class CreateLanModule {
}
