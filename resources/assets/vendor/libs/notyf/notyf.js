import * as NotyfModule from 'notyf';
import 'notyf/notyf.min.css';

try {
  window.Notyf = NotyfModule.Notyf;
} catch (e) {}

export const Notyf = NotyfModule.Notyf;
