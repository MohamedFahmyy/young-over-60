import { Component } from '@angular/core';
import { TranslatePipe } from '@ngx-translate/core';

@Component({
  selector: 'app-vision',
  imports: [TranslatePipe],
  templateUrl: './vision.component.html',
  styleUrl: './vision.component.css',
})
export class VisionComponent {}
