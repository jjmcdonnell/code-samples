let getters = {
  getList: (state) => (listName) => {
    switch(listName){
      case 'ArtThemes':{
        return state.ArtThemes;
      }break;
      case 'Brands':{
        return state.Brands;
      }break;
      case 'Categories':{
        return state.Categories;
      }break;
      case 'Characters':{
        return state.Characters;
      }break;
      case 'Conditions':{
        return state.Conditions;
      }break;
      case 'Cultures':{
        return state.Cultures;
      }break;
      case 'DecorStyles':{
        return state.DecorStyles;
      }break;
      case 'Eras':{
        return state.Eras;
      }break;
      case 'Franchises':{
        return state.Franchises;
      }break;
      case 'Materials':{
        return state.Materials;
      }break;
      case 'Occasions':{
        return state.Occasions;
      }break;
      case 'Shapes':{
        return state.Shapes;
      }break;
      case 'ManufacturerIdTypes':{
        return state.ManufacturerIdTypes;
      }break;
    }
  }
}
export default getters;