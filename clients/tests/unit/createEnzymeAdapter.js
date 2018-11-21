import Enzyme from 'enzyme';
import Adapter from 'enzyme-adapter-react-16';
export const EnzymeAdapter = Enzyme.configure({adapter: new Adapter()});
