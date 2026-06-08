import { Component } from '@angular/core';
import { TranslatePipe } from '@ngx-translate/core';

@Component({
  selector: 'app-phili',
  imports: [TranslatePipe],
  templateUrl: './phili.component.html',
  styleUrl: './phili.component.css',
})
export class PhiliComponent {}
